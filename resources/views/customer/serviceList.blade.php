<!-- Start block -->
<section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5 antialiased">
    <div class="max-w-7xl mx-auto pb-6 sm:px-6 lg:px-8">
        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4">
                <div>
                    <h5 class="mr-3 font-semibold dark:text-white">Customer Service List</h5>
                    <p class="text-gray-500 dark:text-gray-400">Manage all existing services or add a new one</p>
                </div>

                <div
                    class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 items-stretch md:items-center justify-end md:space-x-3 flex-shrink-0">
                    <button type="button" id="addServiceToCustomerButton" data-modal-target="addServiceToCustomer"
                            data-modal-toggle="addServiceToCustomer"
                            class="w-full md:w-auto flex items-center justify-center py-2 px-4 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        <svg class="h-3.5 w-3.5 mr-2" fill="currentColor" viewbox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/>
                        </svg>
                        Add new service
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-4">Service name</th>
                        <th scope="col" class="px-4 py-3">Service Type</th>
                        <th scope="col" class="px-4 py-3">Price</th>
                        <th scope="col" class="px-4 py-3">Active Date</th>
                        <th scope="col" class="px-4 py-3">End Date</th>
                        <th scope="col" class="px-4 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($serviceCustomer as $service)
                        <tr class="border-b dark:border-gray-700">
                            <th scope="row"
                                class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{$service->service_name}}
                            </th>
                            <td class="px-4 py-3">
                                @switch($service->service_type)
                                    @case($service->service_type==='RC')
                                        Reccuring charge
                                        @break
                                    @case($service->service_type==='NRC')
                                        One time charge
                                        @break
                                @endswitch
                            </td>
                            <td class="px-4 py-3">{{$service->service_charge}} MKD</td>
                            <td class="px-4 py-3">{{$service->active_Date}}</td>
                            <td class="px-4 py-3">{{$service->end_date}}</td>

                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white float-right">
                                <div class="flex items-center space-x-4">
                                    <a id="DeleteServiceModalButton"
                                       data-modal-target="DeactivateServiceCustomerModal-{{$service->id}}"
                                       data-modal-toggle="DeactivateServiceCustomerModal-{{$service->id}}"
                                       class="cursor-pointer flex items-center text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-2 text-center dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 -ml-0.5"
                                             viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd"
                                                  d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                  clip-rule="evenodd"/>
                                        </svg>
                                        Deactivate service
                                    </a>
                                </div>
                                @include('customer.serviceDeactivate')
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <nav
                class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4"
                aria-label="Table navigation">
                {{ $serviceCustomer->links() }}
            </nav>
        </div>
    </div>
</section>
<!-- End block -->
@include('customer.serviceAdd')
