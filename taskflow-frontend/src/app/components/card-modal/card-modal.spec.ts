import { ComponentFixture, TestBed } from '@angular/core/testing';

import { CardModal } from './card-modal.component';

describe('CardModal', () => {
  let component: CardModal;
  let fixture: ComponentFixture<CardModal>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [CardModal]
    })
    .compileComponents();

    fixture = TestBed.createComponent(CardModal);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
