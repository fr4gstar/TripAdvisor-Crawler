import { Component } from '@angular/core';

@Component({
  selector: 'my-app',
  template: `
    <header>TripAdvisor-Crawler</header>
    <br />
        <nav>
          <a routerLink="/db" routerLinkActive="active">Database</a>
          <a routerLink="/crawler" routerLinkActive="active">Crawler</a>
        </nav>
        <router-outlet></router-outlet>
    <footer>&copy; 2017 by Munich University of Applied Sciences</footer>
    `,
  styleUrls: ['./app.component.css']
})
export class AppComponent  { name = 'TripAdvisor-Crawler'; }
