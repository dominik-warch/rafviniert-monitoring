import React, { useState } from 'react';
import {MapboxOverlay} from '@deck.gl/mapbox';
import maplibregl from 'maplibre-gl';
import {useControl} from 'react-map-gl';
import { Map as MapGL, NavigationControl } from 'react-map-gl/maplibre';

import Popup from './Popup.jsx';

function DeckGLOverlay(props) {
    const overlay = useControl(() => new MapboxOverlay(props));
    overlay.setProps(props);
    return null;
}

const MapContainer = ({ initialViewState, layers, mapStyle }) => {
    const [selectedFeature, setSelectedFeature] = useState(null);
    const [popupPosition, setPopupPosition] = useState({ x: 0, y: 0 });
    const handleClick = (event) => {
        setSelectedFeature(event.object);
        setPopupPosition({ x: event.x, y: event.y });
    }

    return (
        <>
            <MapGL
                reuseMaps
                mapLib={maplibregl}
                mapStyle={mapStyle}
                width="100%"
                height="100%"
                initialViewState={initialViewState}
                controller={true}

            >
                <DeckGLOverlay
                    layers={layers}
                    onClick={handleClick}
                />
                <NavigationControl />
            </MapGL>
            {selectedFeature && <Popup feature={selectedFeature} position={popupPosition} />}
        </>

    );
};

export default MapContainer;
