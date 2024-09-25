<form action="/search" method="GET" class="d-flex" role="search">
    @csrf

    <input   class="form-control me-2" type="search" placeholder="Search by title or post writer" aria-label="Search" required name="search" value="{{old('search')}}">
    <button class="btn btn-outline-success" type="submit" >Search</button>
</form>
