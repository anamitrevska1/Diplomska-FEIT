<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ Auth::user()->name }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            @include('customer.list')
        </div>
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            @include('service.list')
        </div>
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            @include('discount.list')
        </div>
    </div>
</x-app-layout>
