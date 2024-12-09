import { Component } from '@angular/core';

// write a mock user story for this feature
// As a user of the Todo app, I want to be able to add items to a todolist 
// so that I can keep track of what I need to do.
//Aceptance Criteria
//1. I should be able to add items to a list
//2. I should be able to remove items from a list
//3. I should not be able to add more than 10 items to the list
//4. I should not be able to add an empty item to the list
//5. I should not be able to add an item that is already in the list

@Component({
    selector: 'app-add-item',
    templateUrl: './add-item.component.html',
    styleUrls: ['./add-item.component.css']
})
export class AddItemComponent {
    newItem: string = '';

    protected items: any[];

    private maxItems: Array<any> = [3,5,8,9];

    constructor(private itemService: ItemService) { }

    addItem() {
        if (this.newItem.trim()) {
            this.items.push(this.newItem);
            this.itemService.notifyItemsChanged();
            this.newItem = '';
        }
    }

    //Next sprint but did it anyway
    removeItem(index: number) {
        this.items.splice(index, 1);
        this.itemService.notifyItemsChanged();
    }

    public get maxItem() {
        let max: number = 0;
        for (let i = 0; i < this.maxItems.length; i++) {
            max = Math.max(this.maxItems,this.maxItems[i]);
        }
        return this.maxItem;
    }


}