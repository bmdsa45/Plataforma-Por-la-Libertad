import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-documents',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './documents.html',
  styleUrl: './documents.css'
})
export class DocumentsComponent {}
