import React, { useMemo } from "react";
import config from "../../config.json";

const DynamicLegend = ({ layers, setIsLegendOpen }) => {
    const activeLayers = useMemo(() => layers.filter(layer => layer.props.visible), [layers]);

    const renderGeoJSONLegend = (thresholds) => (
        <div>
            {Object.entries(thresholds).map(([key, { color, label }]) => (
                <div key={key} className="flex items-center mb-1">
                    <div className="w-5 h-5 mr-2 rounded" style={{ backgroundColor: `rgb(${color.join(',')})` }}></div>
                    <span>{label}</span>
                </div>
            ))}
        </div>
    );

    const renderHeatmapLegend = (colorRange) => (
        <div>
            <div className="flex items-center mb-1">
                <div className="flex">
                    {colorRange.map((color, index) => (
                        <div
                            key={index}
                            className="w-5 h-5 mx-1 rounded"
                            style={{ backgroundColor: `rgb(${color.join(',')})` }}
                            title={`Color ${index + 1}`}
                        ></div>
                    ))}
                </div>
            </div>
        </div>
    );

    return (
        <div className="fixed inset-y-0 z-50 flex w-80 transition-transform duration-300 bg-gray-50 transform translate-x-0">
            <div className="z-10 flex flex-col flex-1">
                <div className="flex items-center justify-between flex-shrink-0 w-64 p-4">
                    <button onClick={() => setIsLegendOpen(false)} className="p-1 rounded-lg focus:outline-none focus:ring">
                        <svg
                            className="w-6 h-6"
                            aria-hidden="true"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span className="sr-only">Legende schließen</span>
                    </button>
                </div>

                <div className="flex flex-col flex-1 p-4 mt-4">
                    {activeLayers.map((layer) => {
                        const layerConfig = config.map.layers[layer.id];
                        return (
                            <div key={layer.id} className="mb-4">
                                <span className="block font-bold mb-2">{layerConfig.name}</span>
                                {layerConfig.type === "geojson" && renderGeoJSONLegend(layerConfig.thresholds)}
                                {layerConfig.type === "heatmap" && renderHeatmapLegend(layer.props.colorRange)}
                            </div>
                        );
                    })}
                </div>
            </div>
        </div>
    );
};

export default DynamicLegend;
