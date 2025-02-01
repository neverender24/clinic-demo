<style>
    body {
        background-color: red;
    }
    .inline p {
        display: inline;
    }

    .container {
        width: 60%;
        margin: 0 auto;
        position: relative;
    }
    .table-bordered {
        border: solid black 1px;
    }

    .right-element {
        position: absolute;
        right: 0;
        margin-right: 1rem;
    }

    .main{
        width: 100%;
        height: 100%;
        margin: 0 auto;
    }

    .fi-modal-content:has(> #prescription) {
        background-color:rgb(238, 236, 236);
        padding: 0;
        padding-top: 2rem;
    }

    .paper-background {
        background-color:rgb(255, 255, 255);
        width: 39rem;
        height: 51rem;
        padding-top: 10px;
        /* padding: 7rem 5rem 5rem 1rem; */
    }
    
    header {
        background-image: url("{{asset('images/clinic/mati-clinic.png')}}");
        background-position: center; /* Center the image */
        background-repeat: no-repeat; /* Do not repeat the image */
        background-size: cover; /* Resize the background image to cover the entire container */
        height: 20%;
    }

</style>

<div class="main" id="prescription">
    <div class="container paper-background" style="color:black">
        <header class="header">
            <!-- <img src="{{asset('images/clinic/mati-clinic.png')}}" width="900" alt=""> -->
        </header>
        <p>
            <strong>Name: </strong>{{ $patient->full_name }} 
            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
            <span class="right-element">
            <strong >Date: </strong> {{ now()->format('F j, Y')}}
            </span>
        </p>
        <p><strong>Age:</strong> {{ Carbon\Carbon::parse($patient->birthday)->age }}</p>
        <p>&nbsp;</p>
        @foreach($medicines as $key => $medicine)
            <ol>
                <li>
                    <h1 class="inline"><b>{{ $key + 1}}.)</b> {!! $medicine->name !!} {!! $medicine->brand !!} {!! $medicine->pivot->remarks !!}</h1>
                </li>
            </ol>
        @endforeach
    </div>
    
</div>