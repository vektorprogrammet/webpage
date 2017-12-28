import * as actions from '../actions/department';

export const preferredDepartmentReducer = (state = null, action) => {
  switch (action.type) {
    case actions.SET_PREFERRED_DEPARTMENT:
      return action.payload;
    default:
      return state
  }
};
