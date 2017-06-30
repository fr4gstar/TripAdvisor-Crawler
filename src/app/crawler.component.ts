import {Component, ViewChild, OnInit, AfterViewInit} from '@angular/core';
import {Http} from '@angular/http';
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
export class CrawlerComponent implements OnInit {

  private serverURL = 'http://tripad.ilmbucks.de/';
  // private serverURL = 'http://localhost/';
  private busy;
  private preview = false;
  private previewCity = false;
  private result = false;
  private data = '';
  private previewData = '';
  private previewCityData = '';
  private logNr = 0;
  private log = '';
  private tripadvisorUrl = 'https://www.tripadvisor.';
  private urlCheck = '/Hotel_Review-g';
  private urlCityCheck = '/Hotels-g';
  // Switch to Debug Mode with Console
  private debugMode: Boolean = false;

  private logText(value: string): void {
    this.log += `${this.logNr}: ${value}\n`;
    this.logNr ++;
  }
  /**
   * Initializes the modules
   * @params http for http requests
   **/
  constructor(private http: Http) {
  }

  ngOnInit(): void {
    this.logText(`Log started!`);
  }

  /**
   * Gets the preview data by an url
   * @params url to a hotel
   **/
  getPreview(url: string) {
    let start = (new Date().getTime());
    let end;

    this.result = false;

    if (url.toString() === '') {
      this.preview = false;
      this.previewCity = false;
      this.logText('Waiting for URL!');
    } else if (url.toString().startsWith(this.tripadvisorUrl) &&
      url.toString().includes(this.urlCityCheck)) {
        this.logText('Start Preview Loading for City!' );
        this.logText('URL is valid!' );

        this.busy = this.http.get(this.serverURL + 'preview.php?url=' + url + '&type=city')
          .map(response => response.json())
          .subscribe(result => this.previewCityData = result);

        this.preview = false;
        this.previewCity = true;
        this.logText('Preview loading - URL: ' + url);
    } else if (url.toString().startsWith(this.tripadvisorUrl) &&  url.toString().includes(this.urlCheck)) {
      this.logText('Start Preview Loading!' );
      this.logText('URL is valid!' );

      this.busy = this.http.get(this.serverURL + 'preview.php?url=' + url + '&type=hotel')
                .map(response => response.json())
                .subscribe(result => this.previewData = result);

      this.previewCity = false;
      this.preview = true;
      this.logText('Preview loading - URL: ' + url);
    } else {
      this.preview = false;
      this.previewCity = false;
      this.logText('Invalid URL: ' + url);
    }

    end = new Date().getTime();
    this.logText('End Preview loading!');
    this.logText('Total time: ' + ((end - start) / 1000) + 'ms \n');
  }

  /**
   * Starts the crawling php script to a given hotel/city
   * @params url to a hotel
   **/
  getData(url: string) {
    let start = (new Date().getTime());
    let end;
    this.preview = false;
    this.previewCity = false;

    if (url.toString().startsWith(this.tripadvisorUrl) && url.toString().includes(this.urlCheck)) {
      this.logText('Start Crawling!' );
      this.busy = this.http.get(this.serverURL + 'crawler.php?url=' + url + '&type=hotel')
                .map(response => response.json())
                .subscribe(result => this.data = result);

      this.logText('Crawling - URL: ' + url);
      this.result = true;
    } else if (url.toString().startsWith(this.tripadvisorUrl) && url.toString().includes(this.urlCityCheck)) {
      this.logText('Start City Crawling!' );

      this.busy = this.http.get(this.serverURL + 'crawler.php?url=' + url + '&type=city')
        .map(response => response.json())
        .subscribe(result => this.data = result);

      this.logText('Crawling - URL: ' + url);
      this.result = true;
    }
    end = new Date().getTime();
    this.logText('End Crawling!');
    this.logText('Total time: ' + ((end - start) / 1000) + 'ms \n');
  }
}
