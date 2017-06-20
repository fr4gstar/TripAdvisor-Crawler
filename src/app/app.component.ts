import { Component } from '@angular/core';

@Component({
  selector: 'my-app',
  template: `
    <header>TripAdvisor-Crawler</header>
    <br />
      <my-crawler></my-crawler>
      <my-db></my-db>
    <footer>&copy; 2017 by Munich University of Applied Sciences</footer>
    `,
  styleUrls: ['./app.component.css']
})
export class AppComponent  { name = 'TripAdvisor-Crawler'; }
