<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('GPS Tracker') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Location Map -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Current Location</h3>
                    <div id="map" class="h-96 w-full rounded-lg"></div>
                </div>
            </div>

            <!-- Recent Locations -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Locations</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Time</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Latitude</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Longitude</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($locations as $location)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $location->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $location->latitude }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $location->longitude }}
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

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
        <script>
            // Initialize map
            const map = L.map('map').setView([-6.2088, 106.8456], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            let marker;

            @if ($lastLocation)
                marker = L.marker([{{ $lastLocation->latitude }}, {{ $lastLocation->longitude }}]).addTo(map);
                map.setView([{{ $lastLocation->latitude }}, {{ $lastLocation->longitude }}], 13);
            @endif

            // Update locations every 10 seconds
            function updateLocations() {
                fetch('{{ route('locations.get') }}')
                    .then(response => response.json())
                    .then(locations => {
                        if (locations.length > 0) {
                            const latest = locations[0];

                            if (marker) {
                                map.removeLayer(marker);
                            }

                            marker = L.marker([latest.latitude, latest.longitude]).addTo(map);
                            map.setView([latest.latitude, latest.longitude], 13);
                        }
                    });
            }

            setInterval(updateLocations, 10000);
        </script>
    @endpush
</x-app-layout>
