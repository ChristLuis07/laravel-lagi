<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-white leading-tight">
      Pengaturan Blog
      <a href="{{ route('member.blogs.create') }}" class="bg-blue-400 p-2 rounded-md text-sm">Tambah Tulisan</a>
    </h2>
  </x-slot>
  <x-slot name="headerRight">
    <form action="{{ route('member.blogs.index') }}" method="get">
      <x-text-input id="search" name="search" type="text" class="p-1 m-0 md:w-72 w-80 mt-3 md:mt-0"
        value="{{ request('search') }}" placeholder="masukkan Kata Kunci...">
      </x-text-input>
      <x-secondary-button class="p-1 mt-2" type="submit">Cari</x-secondary-button>
    </form>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-dark shadow-sm sm:rounded-lg overflow-x-auto">
        <div class="p-6 bg-dark text-white border-b border-gray-200">
          <table class="w-full whitespace-no-wrapw-full whitespace-no-wrap table-fixed">
            <thead>
              <tr class="text-center font-bold">
                <td class="border px-6 py-4 w-[80px]">No</td>
                <td class="border px-6 py-4">Judul</td>
                <td class="border px-6 py-4 lg:w-[250px] hidden lg:table-cell">Tanggal</td>
                <td class="border px-6 py-4 lg:w-[100px] hidden lg:table-cell">Status</td>
                <td class="border px-6 py-4 lg:w-[250px] w-[100px]">Aksi</td>
              </tr>
            </thead>
            <tbody>
              @foreach ($data as $key => $value)
                <tr>
                  <td class="border px-6 py-4 text-center">
                    {{ $data->firstItem() + $key }}
                  </td>
                  <td class="border px-6 py-4">
                    {{ $value->title }}
                    <div class="block lg:hidden text-sm text-gray-500">
                      {{ $value->status }} | {{ $value->created_at->isoFormat('dddd, D MMMM Y') }}
                    </div>
                  </td>
                  <td class="border px-6 py-4 text-center text-gray-500 text-sm hidden lg:table-cell">
                    {{ $value->created_at->isoFormat('dddd, D MMMM Y') }}</td>
                  <td class="border px-6 py-4 text-center text-sm hidden lg:table-cell">{{ $value->status }}</td>
                  <td class="border px-6 py-4 text-center">
                    <a href='{{ route('member.blogs.edit', ['post' => $value->id]) }}'
                      class="text-blue-600 hover:text-blue-400 px-2">edit</a>
                    <a target="_blank" href='{{ route('blog-detail', ['slug' => $value->slug]) }}'
                      class="text-blue-600 hover:text-blue-400 px-2">lihat</a>
                    <form class="inline" onsubmit="return confirm('Yakin akan Menghapus data ini?')" method="POST"
                      action="{{ route('member.blogs.destroy', ['post' => $value->id]) }}">
                      @csrf
                      @method('delete')
                      <button type='submit' class='text-red-600 hover:text-red-400 px-2'>
                        hapus
                      </button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="p-5">
          {{ $data->links() }}
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
