<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $customer->first_name}} {{  $customer->last_name}}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto pb-12 sm:px-6 lg:px-8">
            <div class="inline-flex rounded-t-md shadow-sm ">
                <a data-modal-target="updateProductModal" data-modal-toggle="updateProductModal" aria-current="page" class="cursor-pointer px-4 py-2 text-sm font-medium text-blue-700 bg-white border border-gray-200 rounded-tl-lg hover:bg-gray-100 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                    Edit Customer
                </a>
                    <form action={{route('customer.suppress',$customer->id)}} method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="cursor-pointer px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                            @if($customer->no_bill===0)
                                Suppress Customer
                            @else
                                Unsuppress Customer
                            @endif
                        </button>
                    </form>

                <a href="/customer/proforma/{{$customer->id}}"  class="cursor-pointer px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-l border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                    Order Proforma Bill
                </a>

                <a href="#" class="cursor-pointer px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-tr-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                    Delete Customer
                </a>
                <a href="/customer/mainBill/{{$customer->id}}" class="cursor-pointer px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-tr-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:hover:text-white dark:hover:bg-gray-700 dark:focus:ring-blue-500 dark:focus:text-white">
                   Bill Customer
                </a>

            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-b-lg p-8 flex flex-row justify-between">

                <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700 p-3">
                    <div class="flex flex-col pb-3">
                        <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Customer Type</dt>
                        <dd class="text-lg font-semibold">{{$customer->customer_type}}</dd>
                    </div>
                    <div class="flex flex-col py-3">
                        <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Company Name</dt>
                        <dd class="text-lg font-semibold">{{$customer->company_name}}</dd>
                    </div>
                    <div class="flex flex-col pt-3">
                        <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Phone Number</dt>
                        <dd class="text-lg font-semibold">{{$customer->phone}}</dd>
                    </div>
                </dl>

                <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700 p-3">
                    <div class="flex flex-col pb-3">
                        <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Email Address</dt>
                        <dd class="text-lg font-semibold">{{$customer->email}}</dd>
                    </div>
                    <div class="flex flex-col py-3">
                        <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Customer Address</dt>
                        <dd class="text-lg font-semibold">{{$customer->address}} {{$customer->city}} {{$customer->zip}}</dd>
                    </div>
                    <div class="flex flex-col pt-3">
                        <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Billing Frequency</dt>
                        <dd class="text-lg font-semibold">{{$customer->bill_period}}</dd>
                    </div>
                </dl>
                <dl class="max-w-md text-gray-900 divide-y divide-gray-200 dark:text-white dark:divide-gray-700 p-3">
                    <div class="flex flex-col pb-3">
                        <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Is Customer Suppressed</dt>
                        <dd class="text-lg font-semibold">
                            @if ($customer->no_bill === 1)
                                Yes
                            @else
                                No
                            @endif
                        </dd>
                    </div>
                    <div class="flex flex-col py-3">
                        <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Previous Cutoff Date </dt>
                        <dd class="text-lg font-semibold">{{$customer->prev_cutoff_date}}</dd>
                    </div>
                    <div class="flex flex-col pt-3">
                        <dt class="mb-1 text-gray-500 md:text-lg dark:text-gray-400">Open Bill Amount</dt>
                        <dd class="text-lg font-semibold">{{$openBillAmount['total_price']}} MKD</dd>
                    </div>
                </dl>
        </div>
        </div>
        @include('customer.edit')
        @include('customer.serviceList')
        @include('customer.discountList')
        @include('invoice.list')
</x-app-layout>
