import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { Card } from '../models/card';

@Injectable({ providedIn: 'root' })
export class CardService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  getCards(columnId: number): Observable<Card[]> {
    return this.http.get<Card[]>(`${this.apiUrl}/cards?columnId=${columnId}`);
  }

  getCard(id: number): Observable<Card> {
    return this.http.get<Card>(`${this.apiUrl}/cards/${id}`);
  }

  createCard(data: { title: string; columnId: number; description?: string; priority?: string }): Observable<Card> {
    return this.http.post<Card>(`${this.apiUrl}/cards`, data);
  }

  updateCard(id: number, data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/cards/${id}`, data);
  }

  deleteCard(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/cards/${id}`);
  }

  // Checklist
  addChecklistItem(cardId: number, content: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/cards/${cardId}/checklist`, { content });
  }

  updateChecklistItem(cardId: number, itemId: number, data: any): Observable<any> {
    return this.http.put(`${this.apiUrl}/cards/${cardId}/checklist/${itemId}`, data);
  }

  deleteChecklistItem(cardId: number, itemId: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/cards/${cardId}/checklist/${itemId}`);
  }

  // Comments
  addComment(cardId: number, content: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/cards/${cardId}/comments`, { content });
  }

  deleteComment(cardId: number, commentId: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/cards/${cardId}/comments/${commentId}`);
  }

  // Labels
  addLabel(cardId: number, labelId: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/cards/${cardId}/labels/${labelId}`, {});
  }

  removeLabel(cardId: number, labelId: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/cards/${cardId}/labels/${labelId}`);
  }
}