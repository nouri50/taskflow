export interface ChecklistItem {
  id: number;
  content: string;
  isDone: boolean;
  position: number;
}

export interface Comment {
  id: number;
  content: string;
  createdAt: string;
  author: {
    id: number;
    firstName: string;
    lastName: string;
  };
}

export interface Label {
  id: number;
  name: string;
  color: string;
}

export interface Card {
  id: number;
  title: string;
  description?: string;
  priority: 'low' | 'medium' | 'high';
  dueDate?: string;
  position: number;
  assignedTo?: {
    id: number;
    firstName: string;
    lastName: string;
  };
  labels?: Label[];
  checklistItems?: ChecklistItem[];
  comments?: Comment[];
}

export interface Column {
  id: number;
  name: string;
  position: number;
  cards?: Card[];
}