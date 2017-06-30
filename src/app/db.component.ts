import { Component, OnInit } from '@angular/core';
import {GtConfig} from 'angular-generic-table/generic-table/interfaces/gt-config';
import {Http} from '@angular/http';

/**
 * Author: Sergej Bardin / bardin@hm.edu
 * DB Component:
 * - UI for the Tripadvisor DB
 * - Starting PHP Script to export data from db as csv
 **/

@Component({
  selector: 'my-db',
  templateUrl: './db.component.html',
  styleUrls: [ './dashboard.component.css' ]
})
export class DBComponent {

  /**
   * Initializes the modules
   * @params http for http requests
   **/
  const
  constructor(private http: Http) {

  }
}


/*
Copyright 2017 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/
