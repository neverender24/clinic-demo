<style>
    .inline p {
        display: inline;
    }
</style>
<div class="inline">
    @foreach($medicines as $medicine)
        <h1 class="inline">{!! $medicine->name !!} {!! $medicine->brand !!}</h1>
    @endforeach
</div>
