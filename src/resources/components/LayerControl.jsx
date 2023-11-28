import React from 'react';

const LayerControl = ({ layerGroups, layers, onToggle, layerVisibility }) => {
    return (
        <fieldset className="border-b border-t border-gray-300 py-2">
            <legend className="pr-2 text-lg">Themen</legend>
            {layerGroups.map(group => (
                <div className="space-y-1" key={group.groupId}>
                    <div>
                        {group.groupName}
                    </div>
                    {group.layerIds.map(layerId => (
                        <div className="relative flex items-start" key={layerId}>
                            <div className="flex h-6 items-center">
                                <input
                                    id={layerId}
                                    checked={!!layerVisibility[layerId]}
                                    onChange={() => onToggle(layerId)}
                                    name="comments"
                                    type="checkbox"
                                    className="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                />
                            </div>
                            <div className="ml-3 leading-6">
                                <label htmlFor={layerId} className="font-medium text-gray-900">
                                    {layers[layerId].name}
                                </label>
                            </div>
                        </div>
                    ))}
                </div>
            ))}
        </fieldset>
    )
};

export default LayerControl;
