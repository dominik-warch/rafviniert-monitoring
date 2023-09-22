import React, { useEffect, useRef } from 'react';
import maplibregl from 'maplibre-gl';

const Map = () => {
    const mapContainer = useRef(null);

    useEffect(() => {
        const map = new maplibregl.Map({
            container: mapContainer.current,
            style: 'https://sgx.geodatenzentrum.de/gdz_basemapde_vektor/styles/bm_web_col.json',
            center: [0, 0], // starting position [lng, lat]
            zoom: 1 // starting zoom
        });

        return () => map.remove();
    }, []);

    return (
        <div>
            <h1>Map Page</h1>
            <div ref={mapContainer} style={{ width: '100%', height: '500px' }}></div>
        </div>
    );
}

export default Map;
