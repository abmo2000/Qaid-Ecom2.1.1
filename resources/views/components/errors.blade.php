  @if ($errors->any())
          <div class="mb-6 bg-red-900/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside space-y-1">
              @foreach ($errors->all() as $error)
                <li class="text-sm text-white">{{ $error }}</li>
              @endforeach
            </ul>
          </div>
@endif