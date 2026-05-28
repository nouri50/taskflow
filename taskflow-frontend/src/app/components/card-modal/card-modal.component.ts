import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CardService } from '../../services/card.service';
import { BoardService } from '../../services/board.service';
import { Card } from '../../models/card';

@Component({
  selector: 'app-card-modal',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './card-modal.component.html',
  styleUrl: './card-modal.component.scss'
})
export class CardModalComponent implements OnInit {
  @Input() card!: Card;
  @Input() boardId!: number;
  @Output() close = new EventEmitter<void>();

  fullCard: Card | null = null;
  newComment = '';
  newChecklistItem = '';
  isEditing = false;
  editTitle = '';
  editDescription = '';
  editPriority = '';
  editDueDate = '';

  members: any[] = [];
  selectedAssigneeId: number | null = null;

  constructor(
    private cardService: CardService,
    private boardService: BoardService
  ) {}

  ngOnInit() {
    this.loadCard();
    if (this.boardId) {
      this.boardService.getMembers(this.boardId).subscribe({
        next: (members) => this.members = members
      });
    }
  }

  loadCard() {
    this.cardService.getCard(this.card.id).subscribe({
      next: (card) => {
        this.fullCard = card;
        this.editTitle = card.title;
        this.editDescription = card.description || '';
        this.editPriority = card.priority;
        this.editDueDate = card.dueDate || '';
        this.selectedAssigneeId = card.assignedTo?.id ?? null;
      }
    });
  }

  startEdit() {
    this.isEditing = true;
  }

  saveEdit() {
    this.cardService.updateCard(this.card.id, {
      title: this.editTitle,
      description: this.editDescription,
      priority: this.editPriority,
      dueDate: this.editDueDate || null
    }).subscribe({
      next: () => {
        this.isEditing = false;
        this.loadCard();
      }
    });
  }

  cancelEdit() {
    this.isEditing = false;
  }

  addComment() {
    if (!this.newComment.trim()) return;
    this.cardService.addComment(this.card.id, this.newComment).subscribe({
      next: () => {
        this.newComment = '';
        this.loadCard();
      }
    });
  }

  addChecklistItem() {
    if (!this.newChecklistItem.trim()) return;
    this.cardService.addChecklistItem(this.card.id, this.newChecklistItem).subscribe({
      next: () => {
        this.newChecklistItem = '';
        this.loadCard();
      }
    });
  }

  toggleChecklistItem(itemId: number, isDone: boolean) {
    this.cardService.updateChecklistItem(this.card.id, itemId, { isDone: !isDone }).subscribe({
      next: () => this.loadCard()
    });
  }

  deleteChecklistItem(itemId: number) {
    this.cardService.deleteChecklistItem(this.card.id, itemId).subscribe({
      next: () => this.loadCard()
    });
  }

  deleteComment(commentId: number) {
    this.cardService.deleteComment(this.card.id, commentId).subscribe({
      next: () => this.loadCard()
    });
  }

  assignCard(userId: number | null) {
    this.cardService.updateCard(this.card.id, { assignedToId: userId ?? null }).subscribe({
      next: () => this.loadCard()
    });
  }

  getCompletedCount(): number {
    return this.fullCard?.checklistItems?.filter(i => i.isDone).length || 0;
  }

  getTotalCount(): number {
    return this.fullCard?.checklistItems?.length || 0;
  }

  getProgress(): number {
    const total = this.getTotalCount();
    if (total === 0) return 0;
    return (this.getCompletedCount() / total) * 100;
  }
}