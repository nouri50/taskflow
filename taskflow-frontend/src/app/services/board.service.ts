import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';
import { Board } from '../models/board';

@Injectable({ providedIn: 'root' })
export class BoardService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  getBoards(): Observable<Board[]> {
    return this.http.get<Board[]>(`${this.apiUrl}/boards`);
  }

  getBoard(id: number): Observable<Board> {
    return this.http.get<Board>(`${this.apiUrl}/boards/${id}`);
  }

  createBoard(data: { name: string; description?: string }): Observable<Board> {
    return this.http.post<Board>(`${this.apiUrl}/boards`, data);
  }

  updateBoard(id: number, data: Partial<Board>): Observable<any> {
    return this.http.put(`${this.apiUrl}/boards/${id}`, data);
  }

  deleteBoard(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}/boards/${id}`);
  }

  getColumns(boardId: number): Observable<any[]> {
    return this.http.get<any[]>(`${this.apiUrl}/boards/${boardId}/columns`);
  }

  createColumn(boardId: number, data: { name: string; position: number }): Observable<any> {
    return this.http.post(`${this.apiUrl}/boards/${boardId}/columns`, data);
  }
}