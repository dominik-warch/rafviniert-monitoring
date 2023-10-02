import React from 'react';
import DeckGL from '@deck.gl/react';
import maplibregl from 'maplibre-gl';
import { Map as MapGL, NavigationControl } from 'react-map-gl/maplibre';

import config from '../../config.json';

const Map = () => {
    const {initialViewState, mapStyle} = config.map;

    return (
        <div className="flex flex-col h-screen">
            <div
                className="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <nav className="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex justify-between h-16">
                            <div className="sm:flex sm:items-center sm:ml-6">
                                <a href="/dashboard">Zurück</a>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <div className="flex-grow relative">
                <DeckGL
                    initialViewState={initialViewState}
                    controller={true}
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
            </div>
        </div>
    );
}

export default Map;
