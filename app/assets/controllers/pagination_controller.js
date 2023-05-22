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
        this.preparePageButtons();
        this.showCurrentPage();
    }

    preparePageButtons() {
        for (let i = this.maxPage; i >= 1; i--) {
            const newButton = this.pageButtonTarget.cloneNode(true);
            newButton.setAttribute('data-pagination-pagenumber-param', i);
            newButton.innerText = i;
            newButton.classList.remove('d-none');
            this.pageButtonTarget.insertAdjacentHTML('afterend', newButton.outerHTML);
        }
    }

    showCurrentPage() {
        const visibleMinIndex = (this.page - 1) * this.perPageValue;
        const visibleMaxIndex = (this.page - 1) * this.perPageValue + this.perPageValue;

        this.addressTargets.forEach((element, index) => {
            element.hidden = index < visibleMinIndex || index >= visibleMaxIndex
        });
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
}
