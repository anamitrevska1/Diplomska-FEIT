<!-- Start block -->
<section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5 antialiased">
    <div class="max-w-7xl mx-auto pb-6 sm:px-6 lg:px-8">
        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div>
                    <h5 class="mr-3 font-semibold dark:text-white">Invoice List</h5>
                    <p class="text-gray-500 dark:text-gray-400">List of all customer invoices</p>
                </div>
                <div class="w-full md:w-1/2">

                </div>

            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-4">Invoice</th>
                        <th scope="col" class="px-4 py-4">Billing date</th>
                        <th scope="col" class="px-4 py-3">From Date</th>
                        <th scope="col" class="px-4 py-3">To Date</th>
                        <th scope="col" class="px-4 py-3">Amount</th>
                        <th scope="col" class="px-4 py-3">Payment Due Date</th>
                        <th scope="col" class="px-4 py-3">Is Payed</th>
                        <th scope="col" class="px-4 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoiceList as $invoice)
                        <tr class="border-b dark:border-gray-700">
                            <th scope="row"
                                class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                <a href="{{ url('/download/' . $invoice->invoice_id) }}">{{$invoice->invoice_id}}</a>
                            </th>
                            <td class="px-4 py-3">{{$invoice->created_at}}</td>
                            <td class="px-4 py-3">{{$invoice->from_date}}</td>
                            <td class="px-4 py-3">{{$invoice->to_date}}</td>
                            <td class="px-4 py-3">{{$invoice->total_amount}}</td>
                            <td class="px-4 py-3">{{$invoice->payment_due_date}}</td>
                            <td class="px-4 py-3">
                                @if ($invoice->isPaid === 1)
                                    Yes
                                @else
                                    No
                                @endif
                            </td>

                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white float-right">
                                <div class="flex items-center space-x-4">
                                    <a href="/invoice/resend/{{$invoice->invoice_id}}"
                                       class="cursor-pointer flex items-center text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                        Resend Invoice
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <nav
                class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
                aria-label="Table navigation">
                {{ $invoiceList->links() }}
            </nav>
        </div>
    </div>
</section>
<!-- End block -->


