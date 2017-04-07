import {Component, ViewChild, OnInit, AfterViewInit} from '@angular/core';
import {Http} from '@angular/http';
import {
  ShapeOptions,
  LineProgressComponent} from 'angular2-progressbar';

@Component({
  selector: 'my-crawler',
  template: `
    <div>
      <div id="crawl-component">
        <h4>1. Copy URL of single Hotel to the input box and press the "Start" button</h4>

        <form name="urlForm" (ng-submit)="getData()">
          <label>
            <input type="url" class="url" ng-model="urlText" ng-required="true" >
          </label>
          <button id="btn_start" type="button" (click)="getData()">Start</button>
        </form>

       
       <!-- <div *ngFor="let person of data | async"
               (click)="getData()" class="search-result" >
            {{person.first_name}}
        </div>
        -->
        
      </div>
      <hr/>
  
      <h4>2. Progress</h4>
      <div class="line-container">
        <ks-line-progress [options]="lineOptions"></ks-line-progress>
      </div>
      <hr/>
  
      <div id="console">
        <h4>Crawled items</h4>
            <ul>
              <li *ngFor="let person of data">
                {{person.id}} - {{person.first_name}}
              </li>
            </ul>
        <h4>Console</h4>
          <pre>{{log}}</pre>
      </div>
      <hr/>
    </div>`,
  // templateUrl: './crawler.component.html',
  styleUrls: [ './dashboard.component.css'  ]
})
export class CrawlerComponent implements OnInit, AfterViewInit {
  @ViewChild(LineProgressComponent) lineComp: LineProgressComponent;

  private lineOptions: ShapeOptions = {
    strokeWidth: 2,
    easing: 'easeInOut',
    duration: 100,
    color: '#039be5',
    trailColor: '#eee',
    trailWidth: 1,
    text: {
      value: 'Ready to start',
      style: {
        color: '#039be5',
        position: 'center',
        top: 'true'
      }
    },
    svgStyle: { width: '100%' }
  };

  private data = '';
  private logNr: number = 0;
  private log: string = '';

  private logText(value: string): void {
    this.log += `${this.logNr}: ${value}\n`;
    this.logNr ++;
  }

  constructor(private http: Http) {
  }

  ngOnInit(): void {
    this.logText(`Log started!`);
  }
  getData() {
    let start = (new Date().getTime());
    this.logText('Start Crawling!' );
    // this.lineComp.setText();
    this.lineComp.setProgress(0.0);
    this.http.get('http://localhost/test.php/')
              .subscribe(res => this.data = res.json());

    this.lineComp.setText('Finished');
    let end = new Date().getTime();
    this.lineComp.animate(1.0);
    this.logText('End Crawling!');
    this.logText('Total time: ' + ((end - start) / 1000) + 'ms \n');
  }

  ngAfterViewInit() {
    this.lineComp.setProgress(0.0);
    this.logText('after init ... \n');
  }
}
