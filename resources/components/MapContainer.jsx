import React from 'react';
import DeckGL from '@deck.gl/react';
import maplibregl from 'maplibre-gl';
import { Map as MapGL, NavigationControl } from 'react-map-gl/maplibre';

const MapContainer = ({ initialViewState, layers, mapStyle }) => {
    return (
        <DeckGL
            initialViewState={initialViewState}
            controller={true}
            layers={layers}
        >
            <MapGL
                reuseMaps
                mapLib={maplibregl}
                mapStyle={mapStyle}
                width="100%"
                height="100%"
            >
                <NavigationControl />
            </MapGL>
        </DeckGL>
    );
};

export default MapContainer;
