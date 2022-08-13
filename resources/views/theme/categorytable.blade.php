<table class="table table-striped table-bordered zero-configuration">
    <thead>
        <tr>
            <th>#</th>
            <th>Image</th>
            <th>Category Name</th>
            <th>Created at</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($getcategory as $category) {
        ?>
        <tr id="dataid{{$category->id}}">
            <td>{{$category->id}}</td>
            <td><img src='{!! asset("public/images/category/".$category->image) !!}' class='img-fluid' style='max-height: 50px;'></td>
            <td>{{$category->category_name}}</td>
            <td>{{$category->created_at}}</td>
            @if (env('Environment') == 'sendbox')
                <td>
                    @if ($category->is_available == 1)
                        <a class="badge badge-success px-2" onclick="myFunction()" style="color: #fff;">Available</a>
                    @else
                        <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">Unavailable</a>
                    @endif
                </td>
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$category->id}}')" title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="myFunction()" style="color: #fff;">Delete</a>
                    </span>
                </td>
            @else
                <td>
                    @if ($category->is_available == 1)
                        <a class="badge badge-success px-2" onclick="StatusUpdate('{{$category->id}}','2')" style="color: #fff;">Available</a>
                    @else
                        <a class="badge badge-danger px-2" onclick="StatusUpdate('{{$category->id}}','1')" style="color: #fff;">Unavailable</a>
                    @endif
                </td>
                <td>
                    <span>
                        <a href="#" data-toggle="tooltip" data-placement="top" onclick="GetData('{{$category->id}}')" title="" data-original-title="Edit">
                            <span class="badge badge-success">Edit</span>
                        </a>

                        <a class="badge badge-danger px-2" onclick="Delete('{{$category->id}}')" style="color: #fff;">Delete</a>
                    </span>
                </td>
            @endif
        </tr>
        <?php
        }
        ?>
    </tbody>
</table>