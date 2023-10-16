import React, { useEffect, useState, useRef, forwardRef, useImperativeHandle } from 'react';
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

const MapContainer = forwardRef(({ initialViewState, layers, mapStyle, onResize }, ref) => {
    const mapRef = useRef(null)
    const containerRef = useRef(null);

    useEffect(() => {
        const resizeObserver = new ResizeObserver(entries => {
            for (let entry of entries) {
                const { width, height } = entry.contentRect;
                onResize({ width, height }); // Passing dimensions upwards
            }
        });

        if (containerRef.current) {
            resizeObserver.observe(containerRef.current);
        }

        return () => {
            if (containerRef.current) {
                resizeObserver.unobserve(containerRef.current); // Clean up observer on component unmount
            }
        };
    }, [containerRef, onResize]);


    const [selectedFeatureEvent, setSelectedFeatureEvent] = useState(null);
    const [popupPosition, setPopupPosition] = useState({ x: 0, y: 0 });
    const handleClick = (event) => {
        if (!event) return;
        setSelectedFeatureEvent(event);
        setPopupPosition({ x: event.x, y: event.y });
    }

    useImperativeHandle(ref, () => ({
        getMapRef: () => mapRef.current,
    }));

    return (
        <div ref={containerRef} style={{ height: '100vh', width: '100vw' }}>
            <MapGL
                ref={mapRef}
                reuseMaps
                mapLib={maplibregl}
                mapStyle={mapStyle}
                initialViewState={initialViewState}
                preserveDrawingBuffer={true}
                style={{ height: '100vh', width: '100vw' }}
            >
                <DeckGLOverlay
                    layers={layers}
                    interleaved={true}
                    onClick={handleClick}
                />
                <NavigationControl />
            </MapGL>
            {selectedFeatureEvent && <Popup featureEvent={selectedFeatureEvent} position={popupPosition} />}
        </div>

    );
});

export default MapContainer;
