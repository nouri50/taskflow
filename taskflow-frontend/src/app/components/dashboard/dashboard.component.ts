import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { BoardService } from '../../services/board.service';
import { AuthService } from '../../services/auth.service';
import { Board } from '../../models/board';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.scss'
})
export class DashboardComponent implements OnInit {
  boards: Board[] = [];
  showCreateForm = false;
  newBoardName = '';
  newBoardDescription = '';
  loading = false;
  editingBoard: Board | null = null;
  editName = '';
  editDescription = '';

  constructor(
    private boardService: BoardService,
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit() {
    this.loadBoards();
  }

  loadBoards() {
    this.boardService.getBoards().subscribe({
      next: (boards) => this.boards = boards,
      error: (err) => console.error(err)
    });
  }

  createBoard() {
    if (!this.newBoardName.trim()) return;
    this.loading = true;

    this.boardService.createBoard({
      name: this.newBoardName,
      description: this.newBoardDescription
    }).subscribe({
      next: () => {
        this.loadBoards();
        this.showCreateForm = false;
        this.newBoardName = '';
        this.newBoardDescription = '';
        this.loading = false;
      },
      error: () => this.loading = false
    });
  }

  startEdit(board: Board) {
    this.editingBoard = board;
    this.editName = board.name;
    this.editDescription = board.description || '';
  }

  saveBoard(board: Board) {
    this.boardService.updateBoard(board.id, {
      name: this.editName,
      description: this.editDescription
    }).subscribe({
      next: () => {
        this.editingBoard = null;
        this.loadBoards();
      }
    });
  }

  boardToDelete: number | null = null;

deleteBoard(id: number) {
  this.boardToDelete = id;
}

confirmDelete() {
  if (!this.boardToDelete) return;
  this.boardService.deleteBoard(this.boardToDelete).subscribe({
    next: () => {
      this.boardToDelete = null;
      this.loadBoards();
    }
  });
}

  openBoard(id: number) {
    this.router.navigate(['/boards', id]);
  }

  logout() {
    this.authService.logout();
    this.router.navigate(['/login']);
  }
}