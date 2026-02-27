import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'app-proposals',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './proposals.html',
  styleUrl: './proposals.css'
})
export class ProposalsComponent {}
