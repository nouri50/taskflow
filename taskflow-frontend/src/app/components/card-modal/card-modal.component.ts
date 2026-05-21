import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CardService } from '../../services/card.service';
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
  @Output() close = new EventEmitter<void>();

  fullCard: Card | null = null;
  newComment = '';
  newChecklistItem = '';

  constructor(private cardService: CardService) {}

  ngOnInit() {
    this.loadCard();
  }

  loadCard() {
    this.cardService.getCard(this.card.id).subscribe({
      next: (card) => this.fullCard = card
    });
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

  deleteComment(commentId: number) {
    this.cardService.deleteComment(this.card.id, commentId).subscribe({
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