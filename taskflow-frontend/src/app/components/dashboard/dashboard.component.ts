import { Component, OnInit } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { BoardService } from '../../services/board.service';
import { AuthService } from '../../services/auth.service';
import { Board } from '../../models/board';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, FormsModule, ],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.scss'
})
export class DashboardComponent implements OnInit {
  boards: Board[] = [];
  showCreateForm = false;
  newBoardName = '';
  newBoardDescription = '';
  loading = false;

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

  openBoard(id: number) {
    this.router.navigate(['/boards', id]);
  }

  logout() {
    this.authService.logout();
    this.router.navigate(['/login']);
  }
}