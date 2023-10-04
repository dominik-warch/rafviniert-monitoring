import React, { useEffect, useState } from 'react';
import DeckGL from '@deck.gl/react';
import maplibregl from 'maplibre-gl';
import { Map as MapGL, NavigationControl } from 'react-map-gl/maplibre';

import createGeoJsonLayer from "../components/GeoJsonLayerComponent.js";
import LayerControl from "../components/LayerControl.jsx";

import config from '../../config.json';

const Map = () => {
    const {initialViewState, mapStyle, layers: layerConfigs} = config.map;
    const [layers, setLayers] = useState([])
    const [layerVisibility, setLayerVisibility] = useState({});

    const [isSidePaneVisible, setIsSidePaneVisible] = useState(false);

    useEffect(() => {
        const loadLayers = async () => {
            const loadedLayers = await Promise.all(
                layerConfigs.map(layerConfig => createGeoJsonLayer(layerConfig))
            );
            setLayers(loadedLayers);
        };

        loadLayers();
    }, [layerConfigs])

    useEffect(() => {
        const visibility = layers.reduce((acc, layer) => {
            acc[layer.id] = layer.props.visible;
            return acc;
        }, {});
        setLayerVisibility(visibility);
    }, [layers])

    const handleToggleLayer = async (layerId) => {
        const updatedLayers = await Promise.all(
            layers.map(async layer => {
                if (layer.id === layerId) {
                    const originalConfig = layerConfigs.find(config => config.id === layerId);
                    const updatedLayer = await createGeoJsonLayer({
                        ...originalConfig,
                        initialVisible: !layer.props.visible
                    });
                    return updatedLayer;
                }
                return layer;
            })
        );
        setLayers(updatedLayers);
    };

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
            {isSidePaneVisible && (
                <div className="absolute top-20 left-0 w-64 max-h-screen overflow-y-auto z-50 shadow-lg"> {/* Adjusted max-height */}
                    <button
                        onClick={() => setIsSidePaneVisible(!isSidePaneVisible)}
                        className="absolute top-0 left-full bg-white p-2 rounded-r shadow-lg" // Positioned absolutely within the side pane
                    >
                        Toggle Layers
                    </button>
                    <LayerControl
                        layers={layerConfigs}
                        onToggle={handleToggleLayer}
                        layerVisibility={layerVisibility}
                    />
                </div>
            )}
            {!isSidePaneVisible && (
                <button
                    onClick={() => setIsSidePaneVisible(!isSidePaneVisible)}
                    className="absolute top-20 left-4 z-50 bg-white p-2 rounded shadow-lg" // Adjusted top value and z-index
                >
                    Open Layers
                </button>
            )}
            <div className="flex-grow relative">
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
            </div>
        </div>
    );
}

export default Map;
