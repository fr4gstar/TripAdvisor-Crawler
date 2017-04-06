import {Component, ViewChild, Output, EventEmitter, OnInit, AfterViewInit} from '@angular/core';
import {Http} from '@angular/http';
import {
  ShapeOptions,
  LineProgressComponent} from 'angular2-progressbar';

@Component({
  selector: 'my-crawler',
  template: `
    <div>
      <div id="search-component">
        <h4>1. Copy URL of single Hotel to the input box and press the "Start" button</h4>
        <input class="url" type="url" required="true"/>
        <button id="btn_start" type="button">Start</button>
        <div>
          <div *ngFor="let hero of heroes | async"
               (click)="gotoDetail(hero)" class="search-result" >
            {{hero.name}}
          </div>
        </div>
      </div>
      <hr/>
  
      <h4>2. Progress</h4>
      <div class="line-container">
        <ks-line-progress [options]="lineOptions"></ks-line-progress>
      </div>
      <hr/>
  
      <div id="console">
        <h4>X. Console Output</h4>
          <pre>{{log}}</pre>
      </div>
      <hr/>
    </div>`,
  // templateUrl: './crawler.component.html',
  styleUrls: [ './dashboard.component.css'  ]
})
export class CrawlerComponent implements OnInit, AfterViewInit {
  @ViewChild(LineProgressComponent) lineComp: LineProgressComponent;
  @Output() nameChange: EventEmitter<String> = new EventEmitter<String>();

  private lineOptions: ShapeOptions = {
    strokeWidth: 2,
    easing: 'easeInOut',
    duration: 1400,
    color: '#cec9d8',
    trailColor: '#eee',
    trailWidth: 1,
    svgStyle: { width: '100%' }
  };
  private data;
  private log: string = `Log started!'\n`;

  private logText(value: string): void {
    this.log += `${value}\n`;
  }

  constructor(private http: Http) {
  }

  ngOnInit(): void {
    this.getData();
  }
  getData() {
    this.http.get('http://localhost/test.php/')
      .subscribe(res => this.data = res.json());
  }

  ngAfterViewInit() {
    this.lineComp.animate(1);
    this.logText('after init ...');
    this.logText('after init ...');
  }
}
