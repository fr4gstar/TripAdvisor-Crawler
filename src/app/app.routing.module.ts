import { NgModule }             from '@angular/core';
import { RouterModule, Routes } from '@angular/router';

import { CrawlerComponent }   from './crawler.component';
import { DBComponent }   from './db.component';

const routes: Routes = [
  { path: '', redirectTo: '/crawler', pathMatch: 'full' },
  { path: 'db',  component: DBComponent },
  { path: 'crawler',  component: CrawlerComponent }
];

@NgModule({
  imports: [ RouterModule.forRoot(routes) ],
  exports: [ RouterModule ]
})
export class AppRoutingModule {}
