@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent hover:border-indigo-400 dark:border-indigo-600 text-md font-medium leading-5 text-black dark:text-gray-100 focus:outline-none focus:border-blue-700 transition duration-150 ease-in-out no-underline'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-md font-medium leading-5 text-black dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-indigo-600 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out no-underline';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
