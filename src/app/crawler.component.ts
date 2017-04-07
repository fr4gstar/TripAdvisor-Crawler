import {Component, ViewChild, OnInit, AfterViewInit} from '@angular/core';
import {Http} from '@angular/http';
import {
  ShapeOptions,
  LineProgressComponent} from 'angular2-progressbar';
import 'rxjs/add/operator/map';
//import 'rxjs/add/operator/catch';
//import 'rxjs/Rx';

@Component({
  selector: 'my-crawler',
  template: `
    <div>
      <div id="crawl-component">
        <h4>1. Copy URL of single Hotel to the input box and press the "Start" button</h4>

        <form name="urlForm" (ng-submit)="getData()">
          <label>
            <input #url type="url" class="url" ng-model="urlText" ng-required="true" >
          </label>
          <button id="btn_start" type="button" (click)="getData(url.value)">Start</button>
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
        <hr/>
      </div>
      
  
      <div id="console">
        <h4>Crawled items</h4>
        <div id="crawledBox">
            <ul>
              <li *ngFor="let d of data">
                {{d.r_id}} - {{d.title}}
              </li>
            </ul>
        </div>
        <hr/>
        <h4>Console</h4>
        <div id="logBox">
          <pre>{{log}}</pre>
        </div>
        <hr/>
      </div>
      
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
  // private result = '';
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
  getData(url: string) {

    this.lineComp.setProgress(0.0);
    let start = (new Date().getTime());
    let end;

    this.lineComp.setProgress(0.2);
    if (url.toString().startsWith('https://www.tripadvisor.de/')) {
      this.logText('Start Crawling!' );


      this.http.get('http://localhost/crawler.php?url=' + url)
                .map(response => response.json())
                .subscribe(result => this.data = result);

      this.lineComp.setProgress(0.8);
      /*

      this.http.get('http://localhost/crawlerStart.php')
        .map((res: Response) => res.json())
        .subscribe(res => this.data = res.json());
      */
      this.logText('Crawling from URL: ' + url);
      this.lineComp.animate(1.0);
      this.lineComp.setText('Successful');
    } else {
      this.lineComp.animate(0.0);
      this.lineComp.setText('Failed - by URL' + url);
    }
    end = new Date().getTime();
    this.logText('End Crawling!');
    this.logText('Total time: ' + ((end - start) / 1000) + 'ms \n');

  }

  ngAfterViewInit() {
    this.lineComp.setProgress(0.0);
  }
}
