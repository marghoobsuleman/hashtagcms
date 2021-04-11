<Pagination style="padding-left:15px"
        data-paginator="{{$paginator->toJson()}}"
        data-first-item="{{$paginator->firstItem()}}"
        data-last-item="{{$paginator->lastItem()}}"
        data-total="{{$paginator->total()}}"
        data-controller-name="{{request()->module_info->controller_name}}"
>
</Pagination>
