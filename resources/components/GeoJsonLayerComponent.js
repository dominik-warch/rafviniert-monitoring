import { GeoJsonLayer } from '@deck.gl/layers';

const createGeoJsonLayer = async ({ id, endpoint, thresholds, initialVisible }) => {
    try {
        const response = await fetch(endpoint);
        const data = await response.json();

        return new GeoJsonLayer({
            id,
            data,
            pickable: true,
            visible: initialVisible,
            getFillColor: feature => {
                if (!feature.properties) return [255, 255, 255]; // Default color if properties are not defined
                const value = feature.properties.value;
                if (value <= thresholds.low.threshold) return thresholds.low.color;
                if (value <= thresholds.medium.threshold) return thresholds.medium.color;
                if (value > thresholds.high.threshold) return thresholds.high.color;
                return [255, 255, 255]; // Default color
            },
        });
    } catch (error) {
        console.error('Error fetching GeoJSON data:', error);
        return null;
    }
};

export default createGeoJsonLayer;
