import { GeoJsonLayer } from '@deck.gl/layers';
import {HeatmapLayer} from '@deck.gl/aggregation-layers';

const FILTERS = {
    lessThan18: value => value < 18,
    // Add more filters as needed
};

const WEIGHT_ASSIGNMENTS = {
    ageUnder18: props => props.age < 18 ? 1 : 0,
    // Add more weight assignment functions as needed
};


export const createLayer = async ({ type, ...config }) => {
    switch(type) {
        case "geojson":
            return createGeoJsonLayer(config);
        case "heatmap":
            return createHeatmapLayer(config);
        default:
            console.error("Unsupported layer type:", type);
            return null;
    }
};

const createGeoJsonLayer = async ({ id, endpoint, thresholds, initialVisible }) => {
    try {
        const response = await fetch(endpoint);
        const data = await response.json();

        return new GeoJsonLayer({
            id,
            data,
            pickable: true,
            visible: initialVisible,
            lineWidthScale: 5,
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

const createHeatmapLayer = async ({
    id,
    endpoint,
    initialVisible,
    filterProperty = null,
    filterFunction = null,
    weightFunction = null
}) => {
    try {
        const response = await fetch(endpoint);
        const originalData = await response.json();

        let processedData;

        processedData = originalData.features
            .filter(feature => {
                if (!filterProperty || !filterFunction) return true;
                return FILTERS[filterFunction](feature.properties[filterProperty]);
            })
            .map(feature => {
                const coords = feature.geometry.coordinates;
                const weightValue = weightFunction
                    ? WEIGHT_ASSIGNMENTS[weightFunction](feature.properties)
                    : 1;
                return {
                    COORDINATES: coords,
                    WEIGHT: weightValue
                };
            });

        return new HeatmapLayer({
            id,
            data: processedData,
            getPosition: d => d.COORDINATES,
            getWeight: d => d.WEIGHT,
            aggregation: "SUM",
            visible: initialVisible
        });
    } catch (error) {
        console.error('Error fetching data for Heatmap:', error);
        return null;
    }
};
