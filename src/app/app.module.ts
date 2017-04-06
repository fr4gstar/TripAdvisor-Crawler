import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { ProgressBarModule } from 'angular2-progressbar';

import { AppRoutingModule } from './app.routing.module';
import { AppComponent }  from './app.component';
import { DashboardComponent }   from './dashboard.component';
import { CrawlerComponent }   from './crawler.component';
import { DBComponent }   from './db.component';
import { HttpModule, JsonpModule } from '@angular/http';

@NgModule({
  imports:      [
                    BrowserModule,
                    AppRoutingModule,
                    HttpModule,
                    ProgressBarModule,
                    JsonpModule
                ],
  declarations: [
                    AppComponent,
                    DashboardComponent,
                    CrawlerComponent,
                    DBComponent
                ],
  bootstrap:    [ AppComponent ]
})
export class AppModule { }
