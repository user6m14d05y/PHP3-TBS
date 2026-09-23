import { ref } from 'vue';
import axios from 'axios';

const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search';
const USER_AGENT = 'TBS-Flower-Shop/1.0 (PHP3 student project)';

let debounceTimer = null;

export function useGeocode() {
  const geocoding = ref(false);
  const geocodeError = ref('');

  const searchAddress = async (query, { debounceMs = 1000 } = {}) => {
    geocodeError.value = '';
    geocoding.value = true;

    if (debounceTimer) {
      clearTimeout(debounceTimer);
    }

    return new Promise((resolve) => {
      debounceTimer = setTimeout(async () => {
        try {
          const response = await axios.get(NOMINATIM_URL, {
            params: { format: 'json', q: query, limit: 5, addressdetails: 1 },
            headers: { 'User-Agent': USER_AGENT }
          });

          if (response.data && response.data.length > 0) {
            resolve(response.data);
          } else {
            geocodeError.value = 'Không tìm thấy tọa độ cho địa chỉ này, vui lòng nhập chi tiết hơn.';
            resolve([]);
          }
        } catch (error) {
          if (error.response?.status === 403 || error.response?.status === 429) {
            geocodeError.value = 'Dịch vụ tìm tọa độ đang bận, vui lòng thử lại sau vài giây.';
          } else {
            geocodeError.value = 'Không thể kết nối dịch vụ tìm tọa độ, vui lòng thử lại.';
          }
          resolve([]);
        } finally {
          geocoding.value = false;
        }
      }, debounceMs);
    });
  };

  // Geocode từ các trường địa chỉ, trả { latitude, longitude, formatted_address, place_id } hoặc null
  const geocodeAddress = async (address) => {
    const parts = [
      address.address_line,
      address.ward,
      address.district,
      address.city
    ].filter(Boolean);

    if (parts.length === 0) {
      geocodeError.value = 'Vui lòng nhập địa chỉ trước khi tìm tọa độ.';
      return null;
    }

    const results = await searchAddress(parts.join(', '));

    if (!results || results.length === 0) {
      return null;
    }

    const first = results[0];

    return {
      latitude: parseFloat(first.lat),
      longitude: parseFloat(first.lon),
      formatted_address: first.display_name,
      place_id: String(first.place_id || '')
    };
  };

  return { geocoding, geocodeError, searchAddress, geocodeAddress };
}
