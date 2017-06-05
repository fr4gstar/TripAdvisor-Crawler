import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app.routing.module';
import { AppComponent }  from './app.component';
import { CrawlerComponent }   from './crawler.component';
import { DBComponent }   from './db.component';
import { HttpModule, JsonpModule } from '@angular/http';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { BusyModule } from 'angular2-busy';

@NgModule({
  imports:      [
                    BrowserModule,
                    AppRoutingModule,
                    HttpModule,
                    BrowserAnimationsModule,
                    BusyModule,
                    JsonpModule
                ],
  declarations: [
                    AppComponent,
                    CrawlerComponent,
                    DBComponent
                ],
  bootstrap:    [ AppComponent ]
})
export class AppModule { }
