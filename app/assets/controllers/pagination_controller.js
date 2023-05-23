import {Controller} from 'stimulus';

export default class PaginationController extends Controller {
    static values = {
        perPage: {
            type: Number,
            default: 10,
        },
    };

    static targets = ['address', 'pageButton'];

    initialize() {
        this.page = 1
        this.showCurrentPage();
    }

    preparePageButtons() {
        const buttonsWrapper = this.pageButtonTarget.parentNode;
        const newButton = this.pageButtonTarget.cloneNode(true);
        const minPageButtonNumber = this.minPageButtonNumber;
        const maxPageButtonNumber = this.maxPageButtonNumber;
        buttonsWrapper.innerHTML = newButton.outerHTML;

        for (let i = this.maxPage; i >= 1; i--) {
            if (i >= minPageButtonNumber && i <= maxPageButtonNumber) {
                newButton.setAttribute('data-pagination-pagenumber-param', i);
                newButton.innerText = i;
                newButton.classList.remove('d-none', 'btn-primary', 'btn-secondary');
                this.page === i ? newButton.classList.add('btn-primary') : newButton.classList.add('btn-secondary');
                this.pageButtonTarget.insertAdjacentHTML('afterend', newButton.outerHTML);
            }
        }
    }

    showCurrentPage() {
        const visibleMinIndex = (this.page - 1) * this.perPageValue;
        const visibleMaxIndex = (this.page - 1) * this.perPageValue + this.perPageValue;

        this.addressTargets.forEach((element, index) => {
            element.hidden = index < visibleMinIndex || index >= visibleMaxIndex
        });
        this.preparePageButtons();
    }

    showPage(event) {
        this.page = event.params.pagenumber;
        this.showCurrentPage();
    }

    next() {
        if (this.maxPage === this.page) {
            return;
        }

        this.page++;
        this.showCurrentPage()
    }

    previous() {
        if (this.page === 1) {
            return
        }

        this.page--;
        this.showCurrentPage()
    }

    get maxPage() {
        return Number((this.addressTargets.length / this.perPageValue).toPrecision(1));
    }

    get minPageButtonNumber() {
        return this.page > 0 && this.page < 3 ?
            1 : this.page - 2;
    }

    get maxPageButtonNumber() {
        return this.page > 0 && this.page < 3 ?
            (this.maxPage > 5 ? 5 : this.maxPage) :
            this.page + 2;
    }
}
