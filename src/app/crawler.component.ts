import {Component, ViewChild, QueryList, ViewChildren, OnInit, AfterViewInit} from '@angular/core';
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
  
      <div id="console-component">
        <h4>X. Console Output</h4>
        <textarea *ngFor="let person of data" name="console" cols="100px" rows="1">
            {{person.id}} - {{person.first_name}}
  
          </textarea>
      </div>
      <hr/>
    </div>`,
  // templateUrl: './crawler.component.html',
  styleUrls: [ './dashboard.component.css'  ]
})
export class CrawlerComponent implements OnInit, AfterViewInit {
  @ViewChild(LineProgressComponent) lineComp: LineProgressComponent;

  private lineOptions: ShapeOptions = {
    strokeWidth: 4,
    easing: 'easeInOut',
    duration: 1400,
    color: '#3f50ff',
    trailColor: '#eee',
    trailWidth: 1,
    svgStyle: { width: '100%' }
  };
  private data;

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
  }
}
