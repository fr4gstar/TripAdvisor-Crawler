import {Component, ViewChild, OnInit, AfterViewInit} from '@angular/core';
import {Http} from '@angular/http';
import {
  ShapeOptions,
  LineProgressComponent} from 'angular2-progressbar';
import 'rxjs/add/operator/map';

/**
 * Author: Sergej Bardin / bardin@hm.edu
 * Crawler Component:
 * - UI for the Tripadvisor Crawler
 * - Starting the php script for preview of tripadvisor link
 * - Starting the php script for crawling reviews from hotels on tripadvisor
 **/

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

  private preview = false;
  private previewCity = false;
  private data = '';
  private previewData = '';
  private previewCityData = '';
  private logNr: number = 0;
  private log: string = '';
  private urlCheck: string = 'https://www.tripadvisor.de/Hotel_Review-g';
  private urlCityCheck: string = 'https://www.tripadvisor.de/Hotels-g';
  // Switch to Debug Mode with Console
  private debugMode: boolean = false;

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
      this.preview = false;
      this.lineComp.setProgress(0.0);
      this.lineComp.setText('Waiting for URL!');
      this.logText('Waiting for URL!');
    } else if (url.toString().startsWith(this.urlCityCheck)) {
      this.logText('Start Preview Loading for City!' );
      this.logText('URL is valid!' );

      this.http.get('http://localhost/preview.php?url=' + url + '&type=city')
        .map(response => response.json())
        .subscribe(result => this.previewCityData = result);

      this.preview = false;
      this.previewCity = true;
      this.lineComp.animate(0.0);
      this.lineComp.setText('Check Preview!');
      this.logText('Preview loading - URL: ' + url);
    } else if (url.toString().startsWith(this.urlCheck)) {
      this.logText('Start Preview Loading!' );
      this.logText('URL is valid!' );

      this.http.get('http://localhost/preview.php?url=' + url + '&type=hotel')
                .map(response => response.json())
                .subscribe(result => this.previewData = result);

      this.previewCity = false;
      this.preview = true;
      this.lineComp.animate(0.0);
      this.lineComp.setText('Check Preview!');
      this.logText('Preview loading - URL: ' + url);
    } else {
      this.preview = false;
      this.lineComp.setProgress(0.0);
      this.lineComp.setText('Invalid URL!');
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
    if (url.toString().startsWith(this.urlCheck)) {
      this.logText('Start Crawling!' );

      this.http.get('http://localhost/crawler.php?url=' + url + '&type=hotel')
                .map(response => response.json())
                .subscribe(result => this.data = result);

      this.lineComp.animate(0.8);
      this.logText('Crawling - URL: ' + url);
      this.lineComp.animate(1.0);
      this.lineComp.setText('Successful');
    } else if (url.toString().startsWith(this.urlCityCheck)) {
      this.logText('Start City Crawling!' );

      this.http.get('http://localhost/crawler.php?url=' + url + '&type=city')
        .map(response => response.json())
        .subscribe(result => this.data = result);

      this.lineComp.animate(0.8);
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
