import React from 'react';

const LayerControl = ({ layers, onToggle, layerVisibility }) => {
    return (
        <div className="layer-control">
            {layers.map(layer => (
                <div key={layer.id}>
                    <input
                        type="checkbox"
                        id={layer.id}
                        checked={!!layerVisibility[layer.id]}
                        onChange={() => onToggle(layer.id)}
                    />
                    <label htmlFor={layer.id}>{layer.name}</label>
                </div>
            ))}
        </div>
    )
};

export default LayerControl;
