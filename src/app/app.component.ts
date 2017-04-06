import { Component } from '@angular/core';

@Component({
  selector: 'my-app',
  template: `
        <h1>{{name}}</h1>
        <nav>
          <a routerLink="/dashboard" routerLinkActive="active">Dashboard</a>
          <a routerLink="/crawler" routerLinkActive="active">Crawler</a>
          <a routerLink="/db" routerLinkActive="active">Database</a>
        </nav>
        <router-outlet></router-outlet>
        <footer>&copy; 2017 by Munich University of Applied Sciences</footer>
    `,
  styleUrls: ['./app.component.css']
})
export class AppComponent  { name = 'TripAdvisor-Crawler'; }
