import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class ItemService {
  private items: string[] = [];

  //method to notify other components when the items array changes
    private itemsSubject = new Subject<string[]>();

    public notifyItemsChanged() {
        this.itemsSubject.next(this.items);
    }
}