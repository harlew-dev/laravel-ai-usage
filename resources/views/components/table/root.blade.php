<div class="bg-background-secondary border border-border-secondary rounded-lg overflow-hidden">
    <div class="overflow-x-auto w-full">
        <table {{ $attributes->merge(['class' => 'w-full min-w-[1800px] border-collapse']) }}>
            {{ $slot }}
        </table>
    </div>
</div>
