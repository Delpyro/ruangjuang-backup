@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => '
        w-full 
        px-4 py-3 
        text-base text-gray-900 
        bg-gray-50 
        border-2 border-gray-300 
        rounded-xl 
        focus:bg-white 
        focus:border-blue-600 
        focus:ring-0 
        transition-colors 
        duration-200 
        ease-in-out 
        shadow-sm
    '
]) !!}>