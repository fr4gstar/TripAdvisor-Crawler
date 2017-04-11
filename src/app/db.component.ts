import { Component, OnInit } from '@angular/core';
import {GtConfig} from 'angular-generic-table/generic-table/interfaces/gt-config';

@Component({
  selector: 'my-db',
  templateUrl: './db.component.html',
  styleUrls: [ './db.component.css' ]
})
export class DBComponent implements OnInit {

  public configObject: GtConfig<any>;

  public data: Array<{
    r_id: number,
    title: string
  }> = [];

  constructor() {
    this.configObject = {
      settings: [{
        objectKey: 'r_id',
        sort: 'desc',
        columnOrder: 0
      }, {
        objectKey: 'title',
        sort: 'enable',
        columnOrder: 1
      }],
      fields: [{
        name: 'ID',
        objectKey: 'r_id'
      }, {
        name: 'Title',
        objectKey: 'title'
      }],
        data: [{
          'r_id': 1,
          'title': 'Anna'
        }, {
          'r_id': 2,
            'title': 'Julie'
        } , {
          'r_id': 3,
          'title': 'Lillian'
        }, {
          'r_id': 1,
          'title': 'Anna'
        }, {
          'r_id': 2,
          'title': 'Julie'
        } , {
          'r_id': 3,
          'title': 'Lillian'
        },{
          'r_id': 1,
          'title': 'Anna'
        }, {
          'r_id': 2,
          'title': 'Julie'
        } , {
          'r_id': 3,
          'title': 'Lillian'
        },{
          'r_id': 1,
          'title': 'Anna'
        }, {
          'r_id': 2,
          'title': 'Julie'
        } , {
          'r_id': 3,
          'title': 'Lillian'
        }]
    };
  }

  ngOnInit(): void {
  }
}


/*
Copyright 2017 Google Inc. All Rights Reserved.
Use of this source code is governed by an MIT-style license that
can be found in the LICENSE file at http://angular.io/license
*/
