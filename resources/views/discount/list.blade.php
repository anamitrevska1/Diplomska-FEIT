<!-- Start block -->
<section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5 antialiased">
    <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div>
                    <h5 class="mr-3 font-semibold dark:text-white">Discount List</h5>
                    <p class="text-gray-500 dark:text-gray-400">Manage all your existing discounts or add a new one</p>
                </div>
                <div class="w-full md:w-1/2">
                    <form class="flex items-center justify-end">
                        <label for="simple-search" class="sr-only">Search</label>
                        <div class="relative w-full max-w-80">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                     fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                          d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="text" id="discount-search"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 p-2 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                                   placeholder="Search" required="">
                        </div>
                    </form>
                </div>
                <div
                    class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                    <button type="button" id="createDiscountModalButton" data-modal-target="createDiscountModal"
                            data-modal-toggle="createDiscountModal"
                            class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                        </svg>
                        Create new discount
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-4">Discount name</th>
                        <th scope="col" class="px-4 py-3">Price</th>
                        <th scope="col" class="px-4 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($discounts as $discount)
                        <tr class="border-b dark:border-gray-700" id="discount-list-discount-id-{{$discount->id}}">
                            <th scope="row"
                                class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{$discount->name}}
                            </th>
                            <td class="px-4 py-3">
                                @if (isset($discount['discount_amount']))
                                    {{$discount->discount_amount}} MKD
                           @else
                                    {{$discount->discount_percentage}} %
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white float-right">
                                <div class="flex items-center space-x-4">
                                    <a id="editDiscountModalButton" data-modal-target="editDiscountModal-{{$discount->id}}"
                                       data-modal-toggle="editDiscountModal-{{$discount->id}}"
                                       class="cursor-pointer w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-0.5"
                                             viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path
                                                d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"/>
                                            <path fill-rule="evenodd"
                                                  d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        Edit
                                    </a>
                                    <a id="PreviewDiscountModalButton"
                                       data-modal-target="showDiscountModal-{{$discount->id}}"
                                       data-modal-toggle="showDiscountModal-{{$discount->id}}"
                                       class="cursor-pointer py-2 px-3 flex items-center text-sm font-medium text-center text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewbox="0 0 24 24" fill="currentColor"
                                             class="w-4 h-4 mr-2 -ml-0.5">
                                            <path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                  d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z"/>
                                        </svg>
                                        Preview
                                    </a>
                                    <a id="DeleteDiscountModalButton"
                                       data-modal-target="DeleteDiscountModal-{{$discount->id}}"
                                       data-modal-toggle="DeleteDiscountModal-{{$discount->id}}"
                                       class="cursor-pointer flex items-center text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-0.5"
                                             viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                  d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        Delete
                                    </a>
                                </div>
                                @include('discount.edit')
                                @include('discount.show')
                                @include('discount.destroy')
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <nav
                class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
                aria-label="Table navigation">
                {{ $discounts->links() }}
            </nav>
        </div>
    </div>
</section>
<!-- End block -->
@include('discount.create')

<script>
    const discountsList = @json($discounts->items());
    document.getElementById('discount-search').oninput = function(e) {
        const searchValue = e.target.value;
        const filteredDiscounts = discountsList.filter(discount => {
            return discount.name.toLowerCase().includes(searchValue.toLowerCase());
        });

        const discountsToHide = discountsList.filter(discount => {
            return !filteredDiscounts.includes(discount);
        });
        const discountsToShow = discountsList.filter(discount => {
            return filteredDiscounts.includes(discount);
        });

        discountsToHide.forEach(discount => {
            document.getElementById(`discount-list-discount-id-${discount.id}`).style.display = 'none';
        });
        discountsToShow.forEach(discount => {
            document.getElementById(`discount-list-discount-id-${discount.id}`).style.display = 'table-row';
        });
    }
</script>

