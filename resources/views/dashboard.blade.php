<x-app-layout>
    <div class="pt-16 pb-16 bg-gray-100 min-h-screen overflow-x-hidden">
        <div class="max-w-sm mx-auto px-2">
            <!-- Location Map -->
            <div class="bg-white shadow-sm rounded-lg mb-4 relative">
                <div class="p-3">
                    <h3 class="text-base font-medium text-gray-900 mb-2">Current Location</h3>
                    <div id="map" class="h-64 w-full rounded"></div>
                </div>
            </div>

            <!-- Recent Locations -->
            <div class="bg-white shadow-sm rounded-lg relative">
                <div class="p-3">
                    <h3 class="text-base font-medium text-gray-900 mb-2">Recent Locations</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Time
                                    </th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Lat</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Long
                                    </th>
                                    @if ($locations->contains(fn($loc) => $loc->device_id))
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                            Device</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($locations as $location)
                                    <tr>
                                        <td class="px-3 py-2 text-xs text-gray-500">
                                            {{ $location->created_at->format('H:i:s') }}<br>
                                            <span
                                                class="text-xs text-gray-400">{{ $location->created_at->format('Y-m-d') }}</span>
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-500">
                                            {{ number_format($location->latitude, 6) }}
                                        </td>
                                        <td class="px-3 py-2 text-xs text-gray-500">
                                            {{ number_format($location->longitude, 6) }}
                                        </td>
                                        @if ($location->device_id)
                                            <td class="px-3 py-2 text-xs text-gray-500">
                                                {{ $location->device_id }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Initialize map
        const map = L.map('map', {
            zoomControl: false
        }).setView([-6.2088, 106.8456], 13);

        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let marker;

        @if ($lastLocation)
            marker = L.marker([{{ $lastLocation->latitude }}, {{ $lastLocation->longitude }}]).addTo(map);
            map.setView([{{ $lastLocation->latitude }}, {{ $lastLocation->longitude }}], 15);
        @endif

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
                        map.setView([latest.latitude, latest.longitude], 15);

                        window.location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        setInterval(updateLocations, 10000);
    </script>
</x-app-layout>
