import {Component, ViewChild, OnInit, AfterViewInit} from '@angular/core';
import {Http} from '@angular/http';
import {
  ShapeOptions,
  LineProgressComponent} from 'angular2-progressbar';
import 'rxjs/add/operator/map';
// import 'rxjs/add/operator/catch';
// import 'rxjs/Rx';

@Component({
  selector: 'my-crawler',
  templateUrl: './crawler.component.html',
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
  private previewData = '';
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
  getPreview(url: string) {
    let start = (new Date().getTime());
    let end;
    if (url.toString() === '') {
      this.lineComp.setProgress(0.0);
      this.lineComp.setText('Ready to Start!');
      this.logText('Ready to Start!');
    } else if (url.toString().startsWith('https://www.tripadvisor.de/')) {
      this.logText('Start Preview Loading!' );
      this.http.get('http://localhost/preview.php?url=' + url)
        .map(response => response.json())
        .subscribe(result => this.previewData = result);
      this.lineComp.animate(0.1);
      this.lineComp.setText('Preview loaded - Ready to Start!');
      this.logText('Preview loading - URL: ' + url);
    } else {
      this.lineComp.setProgress(0.0);
      this.lineComp.setText('Invalid URL: ' + url);
      this.logText('Invalid URL: ' + url);
    }
    end = new Date().getTime();
    this.logText('End Preview loading!');
    this.logText('Total time: ' + ((end - start) / 1000) + 'ms \n');
  }

  getData(url: string) {

    this.lineComp.setProgress(0.0);
    let start = (new Date().getTime());
    let end;

    this.lineComp.animate(0.2);
    if (url.toString().startsWith('https://www.tripadvisor.de/')) {
      this.logText('Start Crawling!' );


      this.http.get('http://localhost/crawler.php?url=' + url)
                .map(response => response.json())
                .subscribe(result => this.data = result);

      this.lineComp.animate(0.8);
      /*

      this.http.get('http://localhost/crawlerStart.php')
        .map((res: Response) => res.json())
        .subscribe(res => this.data = res.json());
      */
      this.logText('Crawling - URL: ' + url);
      this.lineComp.animate(1.0);
      this.lineComp.setText('Successful');
    } else {
      this.lineComp.setProgress(0.0);
      this.lineComp.setText('Invalid URL: ' + url);
    }
    end = new Date().getTime();
    this.logText('End Crawling!');
    this.logText('Total time: ' + ((end - start) / 1000) + 'ms \n');

  }

  ngAfterViewInit() {
    this.lineComp.setProgress(0.0);
  }
}
