<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Owner Dashboard</h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        
                        {{-- Card Total Users --}}
                        <div class="bg-blue-50 p-6 rounded-lg flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-blue-800">Total Users</h3>
                                <p class="text-3xl font-bold text-blue-600">{{ $userCount }}</p>
                            </div>
                            <i class="fa-solid fa-users text-4xl text-blue-400 opacity-50"></i>
                        </div>
                        
                        {{-- Card Recent Users --}}
                        <div class="bg-green-50 p-6 rounded-lg flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-green-800">Recent Users</h3>
                                <p class="text-3xl font-bold text-green-600">{{ $recentUsers->count() }}</p>
                            </div>
                            <i class="fa-solid fa-clock-rotate-left text-4xl text-green-400 opacity-50"></i>
                        </div>
                        
                    </div>

                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                <i class="fa-solid fa-users-viewfinder mr-2 text-gray-500"></i> Recent Users
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <i class="fa-solid fa-user fa-fw mr-1"></i> Name
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <i class="fa-solid fa-envelope fa-fw mr-1"></i> Email
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <i class="fa-solid fa-briefcase fa-fw mr-1"></i> Role
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                <i class="fa-solid fa-calendar-days fa-fw mr-1"></i> Joined
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($recentUsers as $user)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <i class="fa-solid fa-circle-user text-gray-400 mr-2"></i> {{ $user->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $user->role === 'admin' ? 'purple' : ($user->role === 'owner' ? 'red' : 'green') }}-100 text-{{ $user->role === 'admin' ? 'purple' : ($user->role === 'owner' ? 'red' : 'green') }}-800">
                                                    {{ $user->role }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <i class="fa-regular fa-calendar-check text-gray-400 mr-2"></i> {{ $user->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>