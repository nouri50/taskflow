import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CdkDragDrop, DragDropModule, moveItemInArray, transferArrayItem } from '@angular/cdk/drag-drop';
import { BoardService } from '../../services/board.service';
import { CardService } from '../../services/card.service';
import { CardModalComponent } from '../card-modal/card-modal.component';
import { Card, Column } from '../../models/card';

@Component({
  selector: 'app-board',
  standalone: true,
  imports: [CommonModule, FormsModule, DragDropModule, CardModalComponent],
  templateUrl: './board.component.html',
  styleUrl: './board.component.scss'
})
export class BoardComponent implements OnInit {
  boardId!: number;
  boardName = '';
  columns: Column[] = [];
  newCardTitle: { [columnId: number]: string } = {};
  showAddCard: { [columnId: number]: boolean } = {};
  selectedCard: Card | null = null;

  members: any[] = [];
  showInviteForm = false;
  inviteEmail = '';
  inviteError = '';
  inviteSuccess = '';

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private boardService: BoardService,
    private cardService: CardService
  ) {}

  ngOnInit() {
    this.boardId = Number(this.route.snapshot.paramMap.get('id'));
    this.loadBoard();
    this.loadMembers();
  }

  loadBoard() {
    this.boardService.getBoard(this.boardId).subscribe({
      next: (board) => this.boardName = board.name,
      error: () => this.router.navigate(['/dashboard'])
    });

    this.boardService.getColumns(this.boardId).subscribe({
      next: (columns) => {
        this.columns = columns;
        this.columns.forEach(col => {
          this.cardService.getCards(col.id).subscribe({
            next: (cards) => {
              col.cards = cards;
              col.cards.forEach((card, index) => {
                this.cardService.getCard(card.id).subscribe({
                  next: (fullCard) => col.cards![index] = fullCard
                });
              });
            }
          });
        });
      }
    });
  }

  loadMembers() {
    this.boardService.getMembers(this.boardId).subscribe({
      next: (members) => this.members = members
    });
  }

  inviteMember() {
    if (!this.inviteEmail.trim()) return;
    this.inviteError = '';
    this.inviteSuccess = '';

    this.boardService.inviteMember(this.boardId, this.inviteEmail).subscribe({
      next: () => {
        this.inviteSuccess = 'Membre ajouté !';
        this.inviteEmail = '';
        this.loadMembers();
      },
      error: (err) => {
        this.inviteError = err.error?.message || 'Erreur';
      }
    });
  }

  drop(event: CdkDragDrop<Card[]>, targetColumn: Column) {
    if (event.previousContainer === event.container) {
      moveItemInArray(event.container.data, event.previousIndex, event.currentIndex);
      event.container.data.forEach((card, index) => {
        this.cardService.updateCard(card.id, { position: index }).subscribe();
      });
    } else {
      transferArrayItem(
        event.previousContainer.data,
        event.container.data,
        event.previousIndex,
        event.currentIndex
      );
      const movedCard = event.container.data[event.currentIndex];
      this.cardService.updateCard(movedCard.id, {
        columnId: targetColumn.id,
        position: event.currentIndex
      }).subscribe();
    }
  }

  addCard(column: Column) {
    const title = this.newCardTitle[column.id]?.trim();
    if (!title) return;

    this.cardService.createCard({
      title,
      columnId: column.id,
      priority: 'medium'
    }).subscribe({
      next: (card) => {
        column.cards = [...(column.cards || []), card];
        this.newCardTitle[column.id] = '';
        this.showAddCard[column.id] = false;
      }
    });
  }

  openCard(card: Card) {
    this.selectedCard = card;
  }

  closeCard() {
    this.selectedCard = null;
    this.loadBoard();
  }

  getColumnIds(): string[] {
    return this.columns.map(c => 'col-' + c.id);
  }

  getCheckedCount(card: Card): number {
    return card.checklistItems?.filter(i => i.isDone).length || 0;
  }

  isOverdue(dueDate: string): boolean {
    return new Date(dueDate) < new Date();
  }

  goBack() {
    this.router.navigate(['/dashboard']);
  }
}